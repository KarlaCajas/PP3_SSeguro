<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Factura') }} {{ $invoice->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <td class="px-4 py-3">
                    <input type="hidden" name="items[${index}][id]" value="${item.id || ''}">
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${index}][product_name]" value="${item.product_name}">
                    <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                    <input type="hidden" name="items[${index}][tax_rate]" value="${item.tax_rate || 0}">
                </td>
                <div class="p-6 text-gray-900">
                    @if(!$invoice->isActive())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            Esta factura está cancelada y no se puede editar.
                        </div>
                    @elseif(!$invoice->created_at->isToday())
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            Solo se pueden editar facturas del día actual.
                        </div>
                    @else
                        <form method="POST" action="{{ route('invoices.update', $invoice) }}" id="invoice-form">
                            @csrf
                            @method('PUT')

                            <!-- Información del cliente -->
                            <div class="mb-6 p-4 border rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Información del Cliente</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Cliente Registrado (Opcional)</label>
                                        <select name="customer_id" id="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="fillCustomerData()">
                                            <option value="">Seleccionar cliente registrado...</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" 
                                                        data-name="{{ $customer->name }}" 
                                                        data-email="{{ $customer->email }}"
                                                        {{ old('customer_id', $invoice->customer_id) == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} ({{ $customer->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div></div>
                                    
                                    <div>
                                        <label for="customer_name" class="block text-sm font-medium text-gray-700">Nombre del Cliente *</label>
                                        <input type="text" name="customer_name" id="customer_name" 
                                               value="{{ old('customer_name', $invoice->customer_name) }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        @error('customer_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="customer_email" class="block text-sm font-medium text-gray-700">Email del Cliente</label>
                                        <input type="email" name="customer_email" id="customer_email" 
                                               value="{{ old('customer_email', $invoice->customer_email) }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        @error('customer_email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Productos -->
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
                                            <select id="tax-rate-input" class="w-full rounded-md border-gray-300 shadow-sm">
                                                <option value="0">0% (Exento)</option>
                                                <option value="15">15% (Gravado)</option>
                                            </select>
                                        </div>
                                        <div class="mt-6">
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
                                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="invoice-items" class="divide-y divide-gray-200">
                                                <tr id="no-items-row" style="display: none;">
                                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
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
                                            <span class="font-medium">Subtotal:</span>
                                            <span id="invoice-subtotal">${{ number_format($invoice->subtotal, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between py-2">
                                            <span class="font-medium">IVA:</span>
                                            <span id="invoice-tax">${{ number_format($invoice->tax ?? 0, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between py-2 border-t">
                                            <span class="font-bold text-lg">Total:</span>
                                            <span id="invoice-total" class="font-bold text-lg">${{ number_format($invoice->total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('invoices.show', $invoice) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Cancelar
                                </a>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Actualizar Factura
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemCount = {{ $invoice->items->count() }};
        let invoiceItems = [];
        
        // Cargar items existentes
        @foreach($invoice->items as $item)
            invoiceItems.push({
                id: {{ $item->id }},
                product_id: {{ $item->product_id }},
                product_name: "{{ $item->product_name }}",
                quantity: {{ $item->quantity }},
                unit_price: {{ $item->unit_price }},
                tax_rate: {{ $item->tax_rate ?? 0 }},
                total_price: {{ $item->total_price }},
                stock: {{ $item->product->stock + $item->quantity }} // Stock actual + cantidad usada
            });
        @endforeach

        function fillCustomerData() {
            const select = document.getElementById('customer_id');
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                document.getElementById('customer_name').value = option.dataset.name;
                document.getElementById('customer_email').value = option.dataset.email;
            }
        }

        // Mostrar información del producto seleccionado
        function showProductInfo() {
            const selector = document.getElementById('product-selector');
            const quantityInput = document.getElementById('quantity-input');
            const taxRateInput = document.getElementById('tax-rate-input');
            const productInfo = document.getElementById('product-info');
            const productPrice = document.getElementById('product-price');
            const productStock = document.getElementById('product-stock');
            const productTotal = document.getElementById('product-total');
            
            if (selector.value) {
                const option = selector.options[selector.selectedIndex];
                const price = parseFloat(option.dataset.price);
                const stock = parseInt(option.dataset.stock);
                const quantity = parseInt(quantityInput.value) || 1;
                const taxRate = parseFloat(taxRateInput.value) || 0;
                
                const subtotal = price * quantity;
                const taxAmount = subtotal * (taxRate / 100);
                const total = subtotal + taxAmount;
                
                productPrice.textContent = '$' + price.toFixed(2);
                productStock.textContent = stock + ' unidades';
                productTotal.textContent = '$' + total.toFixed(2);
                
                quantityInput.max = stock;
                if (quantity > stock) {
                    quantityInput.value = stock;
                }
                
                productInfo.classList.remove('hidden');
            } else {
                productInfo.classList.add('hidden');
            }
        }

        // Agregar producto a la factura
        function addProductToInvoice() {
            const selector = document.getElementById('product-selector');
            const quantityInput = document.getElementById('quantity-input');
            const taxRateInput = document.getElementById('tax-rate-input');
            
            if (!selector.value) {
                alert('Por favor seleccione un producto');
                return;
            }
            
            const quantity = parseInt(quantityInput.value);
            if (!quantity || quantity <= 0) {
                alert('Por favor ingrese una cantidad válida');
                return;
            }
            
            const option = selector.options[selector.selectedIndex];
            const productId = parseInt(option.value);
            const productName = option.dataset.name;
            const price = parseFloat(option.dataset.price);
            const stock = parseInt(option.dataset.stock);
            const taxRate = parseFloat(taxRateInput.value);
            
            if (quantity > stock) {
                alert(`Stock insuficiente. Disponible: ${stock} unidades`);
                return;
            }
            
            // Verificar si el producto ya está en la factura
            const existingIndex = invoiceItems.findIndex(item => item.product_id === productId);
            if (existingIndex !== -1) {
                const newQuantity = invoiceItems[existingIndex].quantity + quantity;
                if (newQuantity > stock) {
                    alert(`Stock insuficiente. Ya tienes ${invoiceItems[existingIndex].quantity} unidades. Stock disponible: ${stock}`);
                    return;
                }
                invoiceItems[existingIndex].quantity = newQuantity;
                invoiceItems[existingIndex].tax_rate = taxRate; // Actualizar tasa de IVA
                const subtotal = invoiceItems[existingIndex].quantity * invoiceItems[existingIndex].unit_price;
                const taxAmount = subtotal * (taxRate / 100);
                invoiceItems[existingIndex].total_price = subtotal + taxAmount;
            } else {
                const subtotal = price * quantity;
                const taxAmount = subtotal * (taxRate / 100);
                const totalWithTax = subtotal + taxAmount;
                
                invoiceItems.push({
                    id: null, // Nuevo item
                    product_id: productId,
                    product_name: productName,
                    quantity: quantity,
                    unit_price: price,
                    tax_rate: taxRate,
                    total_price: totalWithTax,
                    stock: stock
                });
            }
            
            renderInvoiceItems();
            updateInvoiceTotals();
            
            // Limpiar selector
            selector.value = '';
            quantityInput.value = 1;
            taxRateInput.value = 0;
            document.getElementById('product-info').classList.add('hidden');
        }

        // Renderizar items de la factura
        function renderInvoiceItems() {
            const tbody = document.getElementById('invoice-items');
            const noItemsRow = document.getElementById('no-items-row');
            
            // Limpiar contenido actual (excepto la fila de "no items")
            Array.from(tbody.children).forEach(row => {
                if (row.id !== 'no-items-row') {
                    row.remove();
                }
            });
            
            if (invoiceItems.length === 0) {
                noItemsRow.style.display = '';
            } else {
                noItemsRow.style.display = 'none';
                
                invoiceItems.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">${item.product_name}</div>
                            <input type="hidden" name="items[${index}][id]" value="${item.id || ''}">
                            <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="items[${index}][product_name]" value="${item.product_name}">
                            <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                            <input type="hidden" name="items[${index}][tax_rate]" value="${item.tax_rate || 0}">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" name="items[${index}][quantity]" value="${item.quantity}" 
                                   min="1" max="${item.stock}" 
                                   class="w-20 rounded border-gray-300 text-center" 
                                   onchange="updateItemQuantity(${index}, this.value)">
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">
                            $${item.unit_price.toFixed(2)}
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                            $${item.total_price.toFixed(2)}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="removeItemFromInvoice(${index})" 
                                    class="text-red-600 hover:text-red-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        }

        // Actualizar cantidad de un item
        function updateItemQuantity(index, newQuantity) {
            console.log('updateItemQuantity called:', index, newQuantity);
            newQuantity = parseInt(newQuantity);
            
            if (!newQuantity || newQuantity <= 0) {
                removeItemFromInvoice(index);
                return;
            }
            
            if (newQuantity > invoiceItems[index].stock) {
                alert(`Stock insuficiente. Máximo disponible: ${invoiceItems[index].stock}`);
                renderInvoiceItems(); // Re-renderizar para revertir el cambio
                return;
            }
            
            invoiceItems[index].quantity = newQuantity;
            const subtotal = invoiceItems[index].quantity * invoiceItems[index].unit_price;
            const taxAmount = subtotal * ((invoiceItems[index].tax_rate || 0) / 100);
            invoiceItems[index].total_price = subtotal + taxAmount;
            
            console.log('Item updated:', invoiceItems[index]);
            
            // Actualizar solo el subtotal en la interfaz
            const row = document.querySelector(`input[name="items[${index}][quantity]"]`).closest('tr');
            const subtotalCell = row.querySelector('td:nth-child(4)');
            subtotalCell.textContent = '$' + invoiceItems[index].total_price.toFixed(2);
            
            updateInvoiceTotals();
        }

        // Remover item de la factura
        function removeItemFromInvoice(index) {
            invoiceItems.splice(index, 1);
            renderInvoiceItems();
            updateInvoiceTotals();
        }

        // Actualizar totales de la factura
        function updateInvoiceTotals() {
            console.log('updateInvoiceTotals called, items:', invoiceItems);
            let subtotal = 0;
            let totalTax = 0;
            
            invoiceItems.forEach(item => {
                const itemSubtotal = item.quantity * item.unit_price;
                const itemTax = itemSubtotal * ((item.tax_rate || 0) / 100);
                subtotal += itemSubtotal;
                totalTax += itemTax;
                console.log('Item calculation:', {
                    product: item.product_name,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    tax_rate: item.tax_rate,
                    itemSubtotal,
                    itemTax
                });
            });
            
            const total = subtotal + totalTax;
            
            console.log('Final totals:', { subtotal, totalTax, total });
            
            const subtotalElement = document.getElementById('invoice-subtotal');
            const taxElement = document.getElementById('invoice-tax');
            const totalElement = document.getElementById('invoice-total');
            
            if (subtotalElement) subtotalElement.textContent = '$' + subtotal.toFixed(2);
            if (taxElement) taxElement.textContent = '$' + totalTax.toFixed(2);
            if (totalElement) totalElement.textContent = '$' + total.toFixed(2);
        }

        // Event listeners
        document.getElementById('product-selector').addEventListener('change', showProductInfo);
        document.getElementById('quantity-input').addEventListener('input', showProductInfo);
        document.getElementById('tax-rate-input').addEventListener('change', showProductInfo);

        // Inicializar la vista al cargar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');
            console.log('Initial invoiceItems:', invoiceItems);
            
            // Verificar que los elementos necesarios existan
            const requiredElements = [
                'invoice-items',
                'invoice-subtotal', 
                'invoice-tax',
                'invoice-total',
                'product-selector',
                'quantity-input',
                'tax-rate-input'
            ];
            
            const missingElements = requiredElements.filter(id => !document.getElementById(id));
            if (missingElements.length > 0) {
                console.error('Missing elements:', missingElements);
            }
            
            renderInvoiceItems();
            updateInvoiceTotals();
        });

        // Validar antes de enviar
        document.getElementById('invoice-form').addEventListener('submit', function(e) {
            console.log('Form submission attempted');
            console.log('Current invoiceItems:', invoiceItems);
            
            if (invoiceItems.length === 0) {
                alert('Debe agregar al menos un producto a la factura');
                e.preventDefault();
                return;
            }
            
            // Validación adicional de stock
            for (let item of invoiceItems) {
                if (item.quantity > item.stock) {
                    alert(`Stock insuficiente para ${item.product_name}. Disponible: ${item.stock}, solicitado: ${item.quantity}`);
                    e.preventDefault();
                    return;
                }
            }
            
            // Validar que todos los campos requeridos estén presentes
            const form = document.getElementById('invoice-form');
            const formData = new FormData(form);
            console.log('Form data to be submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key, ':', value);
            }
        });


    </script>
</x-app-layout>
