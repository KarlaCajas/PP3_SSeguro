// 🚀 Cliente API JavaScript para Sistema de Facturas Laravel
// Funcionalidades: CRUD completo, manejo de errores, UI responsive

class ApiClient {
    constructor() {
        this.baseUrl = 'http://127.0.0.1:8000';
        this.token = 'admin-test-token';
        this.version = 'v2';
    }

    // 🔧 Configuración y utilidades
    getHeaders() {
        return {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.token}`,
            'Accept': 'application/json'
        };
    }

    getUrl(endpoint) {
        return `${this.baseUrl}/api/${this.version}/${endpoint}`;
    }

    // 🌐 Método genérico para hacer requests
    async makeRequest(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers: this.getHeaders(),
                ...options
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
            }

            return data;
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }

    // 📡 Probar conexión
    async testConnection() {
        try {
            showStatus('Probando conexión...', 'info');
            const data = await this.makeRequest(this.getUrl('user'));
            showStatus(`✅ Conexión exitosa. Usuario: ${data.name || 'N/A'}`, 'success');
            return true;
        } catch (error) {
            showStatus(`❌ Error de conexión: ${error.message}`, 'error');
            return false;
        }
    }

    // 📦 PRODUCTOS
    async getProductos(page = 1) {
        try {
            const data = await this.makeRequest(this.getUrl(`productos?page=${page}`));
            displayProductos(data.data || data);
            showStatus('✅ Productos cargados exitosamente', 'success');
        } catch (error) {
            showStatus(`❌ Error al cargar productos: ${error.message}`, 'error');
        }
    }

    async createProducto(productData) {
        try {
            const data = await this.makeRequest(this.getUrl('productos'), {
                method: 'POST',
                body: JSON.stringify(productData)
            });
            showStatus('✅ Producto creado exitosamente', 'success');
            closeModal();
            this.getProductos(); // Recargar lista
            return data;
        } catch (error) {
            showStatus(`❌ Error al crear producto: ${error.message}`, 'error');
            throw error;
        }
    }

    async updateProducto(id, productData) {
        try {
            const data = await this.makeRequest(this.getUrl(`productos/${id}`), {
                method: 'PUT',
                body: JSON.stringify(productData)
            });
            showStatus('✅ Producto actualizado exitosamente', 'success');
            closeModal();
            this.getProductos(); // Recargar lista
            return data;
        } catch (error) {
            showStatus(`❌ Error al actualizar producto: ${error.message}`, 'error');
            throw error;
        }
    }

    async deleteProducto(id) {
        if (!confirm('¿Estás seguro de eliminar este producto?')) return;
        
        try {
            await this.makeRequest(this.getUrl(`productos/${id}`), {
                method: 'DELETE'
            });
            showStatus('✅ Producto eliminado exitosamente', 'success');
            this.getProductos(); // Recargar lista
        } catch (error) {
            showStatus(`❌ Error al eliminar producto: ${error.message}`, 'error');
        }
    }

    // 📂 CATEGORÍAS
    async getCategorias() {
        try {
            const data = await this.makeRequest(this.getUrl('categorias'));
            displayCategorias(data.data || data);
            showStatus('✅ Categorías cargadas exitosamente', 'success');
        } catch (error) {
            showStatus(`❌ Error al cargar categorías: ${error.message}`, 'error');
        }
    }

    // 👥 CLIENTES
    async getClientes(page = 1) {
        try {
            const data = await this.makeRequest(this.getUrl(`clientes?page=${page}`));
            displayClientes(data.data || data);
            showStatus('✅ Clientes cargados exitosamente', 'success');
        } catch (error) {
            showStatus(`❌ Error al cargar clientes: ${error.message}`, 'error');
        }
    }

    async createCliente(clientData) {
        try {
            const data = await this.makeRequest(this.getUrl('clientes'), {
                method: 'POST',
                body: JSON.stringify(clientData)
            });
            showStatus('✅ Cliente creado exitosamente', 'success');
            closeModal();
            this.getClientes(); // Recargar lista
            return data;
        } catch (error) {
            showStatus(`❌ Error al crear cliente: ${error.message}`, 'error');
            throw error;
        }
    }

    // 🧾 FACTURAS
    async getFacturas(page = 1) {
        try {
            const data = await this.makeRequest(this.getUrl(`facturas?page=${page}`));
            displayFacturas(data.data || data);
            showStatus('✅ Facturas cargadas exitosamente', 'success');
        } catch (error) {
            showStatus(`❌ Error al cargar facturas: ${error.message}`, 'error');
        }
    }

    async createFactura(invoiceData) {
        try {
            const data = await this.makeRequest(this.getUrl('facturas'), {
                method: 'POST',
                body: JSON.stringify(invoiceData)
            });
            showStatus('✅ Factura creada exitosamente', 'success');
            closeModal();
            this.getFacturas(); // Recargar lista
            return data;
        } catch (error) {
            showStatus(`❌ Error al crear factura: ${error.message}`, 'error');
            throw error;
        }
    }

    // 💳 PAGOS
    async getPagos(page = 1) {
        try {
            const data = await this.makeRequest(this.getUrl(`pagos?page=${page}`));
            displayPagos(data.data || data);
            showStatus('✅ Pagos cargados exitosamente', 'success');
        } catch (error) {
            showStatus(`❌ Error al cargar pagos: ${error.message}`, 'error');
        }
    }

    // 📋 MIS FACTURAS (Cliente)
    async getMisFacturas() {
        try {
            const data = await this.makeRequest(this.getUrl('mis-facturas'));
            displayMisFacturas(data.data || data);
            showStatus('✅ Mis facturas cargadas exitosamente', 'success');
        } catch (error) {
            showStatus(`❌ Error al cargar mis facturas: ${error.message}`, 'error');
        }
    }
}

// 🌐 Instancia global del cliente API
const apiClient = new ApiClient();

// 🎛️ Funciones de control de UI
function updateApiSettings() {
    apiClient.token = document.getElementById('apiToken').value || 'admin-test-token';
    apiClient.version = document.getElementById('apiVersion').value || 'v2';
    apiClient.baseUrl = document.getElementById('baseUrl').value || 'http://127.0.0.1:8000';
}

function showStatus(message, type = 'info') {
    const statusDiv = document.getElementById('connectionStatus');
    statusDiv.innerHTML = `<div class="status ${type}">${message}</div>`;
    
    // Auto-limpiar después de 5 segundos para mensajes de éxito
    if (type === 'success') {
        setTimeout(() => {
            statusDiv.innerHTML = '';
        }, 5000);
    }
}

function clearStatus() {
    document.getElementById('connectionStatus').innerHTML = '';
    // Limpiar también todos los resultados
    ['productosResult', 'categoriasResult', 'clientesResult', 'facturasResult', 'pagosResult', 'misFacturasResult'].forEach(id => {
        document.getElementById(id).innerHTML = '';
    });
}

function showTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Desactivar todos los botones de tab
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Mostrar el tab seleccionado
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}

// 🏠 Funciones principales llamadas desde HTML
function testConnection() {
    updateApiSettings();
    apiClient.testConnection();
}

function getProductos() {
    updateApiSettings();
    apiClient.getProductos();
}

function getCategorias() {
    updateApiSettings();
    apiClient.getCategorias();
}

function getClientes() {
    updateApiSettings();
    apiClient.getClientes();
}

function getFacturas() {
    updateApiSettings();
    apiClient.getFacturas();
}

function getPagos() {
    updateApiSettings();
    apiClient.getPagos();
}

function getMisFacturas() {
    updateApiSettings();
    apiClient.getMisFacturas();
}

// 🎨 Funciones de renderizado de datos
function displayProductos(data) {
    const container = document.getElementById('productosResult');
    
    if (!data || !data.data || data.data.length === 0) {
        container.innerHTML = '<p>No se encontraron productos.</p>';
        return;
    }

    let html = `
        <div class="data-grid">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.data.forEach(producto => {
        const isActive = producto.is_active !== undefined ? producto.is_active : !producto.deleted_at;
        html += `
            <tr>
                <td>${producto.id}</td>
                <td>${producto.name || 'Sin nombre'}</td>
                <td>$${parseFloat(producto.price || 0).toFixed(2)}</td>
                <td>${producto.stock}</td>
                <td>${producto.category?.name || 'N/A'}</td>
                <td>${isActive ? '✅ Activo' : '❌ Inactivo'}</td>
                <td>
                    <button class="btn btn-warning" onclick="editProducto(${producto.id})">✏️ Editar</button>
                    <button class="btn btn-danger" onclick="apiClient.deleteProducto(${producto.id})">🗑️ Eliminar</button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
        <div style="margin-top: 10px;">
            <small>Total: ${data.total || data.data.length} productos</small>
        </div>
    `;
    
    container.innerHTML = html;
}

function displayCategorias(data) {
    const container = document.getElementById('categoriasResult');
    
    if (!data || data.length === 0) {
        container.innerHTML = '<p>No se encontraron categorías.</p>';
        return;
    }

    let html = `
        <div class="data-grid">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha Creación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.forEach(categoria => {
        const isActive = !categoria.deleted_at; // Si no está eliminada, está activa
        const fechaCreacion = categoria.created_at ? 
            new Date(categoria.created_at).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }) : 'N/A';
        
        html += `
            <tr>
                <td>${categoria.id}</td>
                <td>${categoria.name || 'Sin nombre'}</td>
                <td>${fechaCreacion}</td>
                <td>${isActive ? '✅ Activa' : '❌ Inactiva'}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
        <div style="margin-top: 10px;">
            <small>Total: ${data.length} categorías</small>
        </div>
    `;
    
    container.innerHTML = html;
}

function displayClientes(data) {
    const container = document.getElementById('clientesResult');
    
    if (!data || !data.data || data.data.length === 0) {
        container.innerHTML = '<p>No se encontraron clientes.</p>';
        return;
    }

    let html = `
        <div class="data-grid">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.data.forEach(cliente => {
        html += `
            <tr>
                <td>${cliente.id}</td>
                <td>${cliente.name}</td>
                <td>${cliente.email}</td>
                <td>${cliente.phone || 'N/A'}</td>
                <td>${cliente.is_active ? '✅ Activo' : '❌ Inactivo'}</td>
                <td>${cliente.role_name || 'Sin rol'}</td>
                <td>
                    <button class="btn btn-warning" onclick="editCliente(${cliente.id})">✏️ Editar</button>
                    <button class="btn btn-danger" onclick="deleteCliente(${cliente.id})">🗑️ Eliminar</button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

function displayFacturas(data) {
    const container = document.getElementById('facturasResult');
    
    if (!data || !data.data || data.data.length === 0) {
        container.innerHTML = '<p>No se encontraron facturas.</p>';
        return;
    }

    let html = `
        <div class="data-grid">
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.data.forEach(factura => {
        const fechaCreacion = factura.created_at ? 
            new Date(factura.created_at).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit', 
                day: '2-digit'
            }) : 'N/A';
            
        html += `
            <tr>
                <td>${factura.invoice_number}</td>
                <td>${factura.customer_name}</td>
                <td>$${parseFloat(factura.total).toFixed(2)}</td>
                <td>${getEstadoFactura(factura.status)}</td>
                <td>${fechaCreacion}</td>
                <td>
                    <button class="btn" onclick="viewFactura(${factura.id})">👁️ Ver</button>
                    <button class="btn btn-warning" onclick="editFactura(${factura.id})">✏️ Editar</button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

function displayPagos(data) {
    const container = document.getElementById('pagosResult');
    
    if (!data || !data.data || data.data.length === 0) {
        container.innerHTML = '<p>No se encontraron pagos.</p>';
        return;
    }

    let html = `
        <div class="data-grid">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Factura</th>
                        <th>Monto</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.data.forEach(pago => {
        const fechaCreacion = pago.created_at ? 
            new Date(pago.created_at).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }) : 'N/A';
            
        html += `
            <tr>
                <td>${pago.id}</td>
                <td>${pago.invoice_number || pago.invoice?.invoice_number || 'N/A'}</td>
                <td>$${parseFloat(pago.monto).toFixed(2)}</td>
                <td>${pago.tipo_pago}</td>
                <td>${getEstadoPago(pago.estado)}</td>
                <td>${fechaCreacion}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

function displayMisFacturas(data) {
    const container = document.getElementById('misFacturasResult');
    
    if (!data || data.length === 0) {
        container.innerHTML = '<p>No tienes facturas.</p>';
        return;
    }

    let html = `
        <div class="data-grid">
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Items</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.forEach(factura => {
        const fechaCreacion = factura.created_at ? 
            new Date(factura.created_at).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }) : 'N/A';
            
        html += `
            <tr>
                <td>${factura.invoice_number}</td>
                <td>$${parseFloat(factura.total).toFixed(2)}</td>
                <td>${getEstadoFactura(factura.status)}</td>
                <td>${fechaCreacion}</td>
                <td>${factura.items ? factura.items.length : 0} items</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

// 🎨 Funciones auxiliares
function getEstadoFactura(status) {
    const estados = {
        'draft': '📝 Borrador',
        'sent': '📧 Enviada', 
        'paid': '✅ Pagada',
        'cancelled': '❌ Cancelada'
    };
    return estados[status] || status;
}

function getEstadoPago(estado) {
    const estados = {
        'pendiente': '⏳ Pendiente',
        'aprobado': '✅ Aprobado',
        'rechazado': '❌ Rechazado'
    };
    return estados[estado] || estado;
}

// 🔧 Funciones de modal y formularios
function showModal(title, content) {
    const modal = document.getElementById('modal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.innerHTML = `
        <h2>${title}</h2>
        ${content}
    `;
    
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

function showCreateProductModal() {
    const content = `
        <form onsubmit="createProducto(event)">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Descripción:</label>
                <textarea name="description"></textarea>
            </div>
            <div class="form-group">
                <label>Precio:</label>
                <input type="number" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" name="stock" required>
            </div>
            <div class="form-group">
                <label>Categoría ID:</label>
                <input type="number" name="category_id" required>
            </div>
            <button type="submit" class="btn btn-success">Crear Producto</button>
            <button type="button" class="btn" onclick="closeModal()">Cancelar</button>
        </form>
    `;
    showModal('➕ Crear Nuevo Producto', content);
}

function showCreateClientModal() {
    const content = `
        <form onsubmit="createCliente(event)">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="text" name="phone">
            </div>
            <div class="form-group">
                <label>Dirección:</label>
                <textarea name="address"></textarea>
            </div>
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-success">Crear Cliente</button>
            <button type="button" class="btn" onclick="closeModal()">Cancelar</button>
        </form>
    `;
    showModal('➕ Crear Nuevo Cliente', content);
}

// 📝 Event handlers para formularios
function createProducto(event) {
    event.preventDefault();
    updateApiSettings();
    
    const formData = new FormData(event.target);
    const productData = {
        name: formData.get('name'),
        description: formData.get('description'),
        price: parseFloat(formData.get('price')),
        stock: parseInt(formData.get('stock')),
        category_id: parseInt(formData.get('category_id'))
    };
    
    apiClient.createProducto(productData);
}

function editProducto(id) {
    // Mostrar modal de edición con datos precargados
    showModal('✏️ Editar Producto', `
        <div class="status info">Cargando datos del producto...</div>
        <div id="editProductForm"></div>
    `);
    
    updateApiSettings();
    
    // Obtener datos del producto
    apiClient.makeRequest(apiClient.getUrl(`productos/${id}`))
        .then(response => {
            const producto = response.data || response;
            const content = `
                <form onsubmit="updateProducto(event, ${id})">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="name" value="${producto.name || ''}" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="description">${producto.description || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Precio:</label>
                        <input type="number" name="price" step="0.01" value="${producto.price || ''}" required>
                    </div>
                    <div class="form-group">
                        <label>Stock:</label>
                        <input type="number" name="stock" value="${producto.stock || ''}" required>
                    </div>
                    <div class="form-group">
                        <label>Categoría ID:</label>
                        <input type="number" name="category_id" value="${producto.category_id || ''}" required>
                    </div>
                    <button type="submit" class="btn btn-success">✅ Actualizar</button>
                    <button type="button" class="btn" onclick="closeModal()">❌ Cancelar</button>
                </form>
            `;
            document.getElementById('editProductForm').innerHTML = content;
        })
        .catch(error => {
            document.getElementById('editProductForm').innerHTML = 
                `<div class="status error">Error al cargar producto: ${error.message}</div>`;
        });
}

function updateProducto(event, id) {
    event.preventDefault();
    updateApiSettings();
    
    const formData = new FormData(event.target);
    const productData = {
        name: formData.get('name'),
        description: formData.get('description'),
        price: parseFloat(formData.get('price')),
        stock: parseInt(formData.get('stock')),
        category_id: parseInt(formData.get('category_id'))
    };
    
    apiClient.updateProducto(id, productData);
}

function createCliente(event) {
    event.preventDefault();
    updateApiSettings();
    
    const formData = new FormData(event.target);
    const clientData = {
        name: formData.get('name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        address: formData.get('address'),
        password: formData.get('password'),
        is_active: true
    };
    
    apiClient.createCliente(clientData);
}

function editCliente(id) {
    showModal('✏️ Editar Cliente', `
        <div class="status info">Cargando datos del cliente...</div>
        <div id="editClientForm"></div>
    `);
    
    updateApiSettings();
    
    apiClient.makeRequest(apiClient.getUrl(`clientes/${id}`))
        .then(response => {
            const cliente = response.data || response;
            const content = `
                <form onsubmit="updateCliente(event, ${id})">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="name" value="${cliente.name || ''}" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="${cliente.email || ''}" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="text" name="phone" value="${cliente.phone || ''}">
                    </div>
                    <div class="form-group">
                        <label>Dirección:</label>
                        <textarea name="address">${cliente.address || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Estado:</label>
                        <select name="is_active">
                            <option value="1" ${cliente.is_active ? 'selected' : ''}>Activo</option>
                            <option value="0" ${!cliente.is_active ? 'selected' : ''}>Inactivo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">✅ Actualizar</button>
                    <button type="button" class="btn" onclick="closeModal()">❌ Cancelar</button>
                </form>
            `;
            document.getElementById('editClientForm').innerHTML = content;
        })
        .catch(error => {
            document.getElementById('editClientForm').innerHTML = 
                `<div class="status error">Error al cargar cliente: ${error.message}</div>`;
        });
}

function updateCliente(event, id) {
    event.preventDefault();
    updateApiSettings();
    
    const formData = new FormData(event.target);
    const clientData = {
        name: formData.get('name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        address: formData.get('address'),
        is_active: parseInt(formData.get('is_active'))
    };
    
    apiClient.makeRequest(apiClient.getUrl(`clientes/${id}`), {
        method: 'PUT',
        body: JSON.stringify(clientData)
    })
    .then(() => {
        showStatus('✅ Cliente actualizado exitosamente', 'success');
        closeModal();
        apiClient.getClientes();
    })
    .catch(error => {
        showStatus(`❌ Error al actualizar cliente: ${error.message}`, 'error');
    });
}

function deleteCliente(id) {
    if (!confirm('¿Estás seguro de eliminar este cliente?')) return;
    
    updateApiSettings();
    
    apiClient.makeRequest(apiClient.getUrl(`clientes/${id}`), {
        method: 'DELETE'
    })
    .then(() => {
        showStatus('✅ Cliente eliminado exitosamente', 'success');
        apiClient.getClientes();
    })
    .catch(error => {
        showStatus(`❌ Error al eliminar cliente: ${error.message}`, 'error');
    });
}

// 🔄 Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal cuando se hace clic fuera
    window.onclick = function(event) {
        const modal = document.getElementById('modal');
        if (event.target === modal) {
            closeModal();
        }
    }
    
    // Auto-actualizar configuración cuando cambian los campos
    ['apiToken', 'apiVersion', 'baseUrl'].forEach(id => {
        document.getElementById(id).addEventListener('change', updateApiSettings);
    });
    
    console.log('🚀 Cliente API inicializado correctamente');
});

// 🧾 Funciones de Facturas
function viewFactura(id) {
    showModal('👁️ Ver Factura', `
        <div class="status info">Cargando detalles de la factura...</div>
        <div id="facturaDetails"></div>
    `);
    
    updateApiSettings();
    
    apiClient.makeRequest(apiClient.getUrl(`facturas/${id}`))
        .then(response => {
            const factura = response.data || response;
            const content = `
                <div class="card">
                    <h3>📄 Factura ${factura.invoice_number}</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <strong>Cliente:</strong> ${factura.customer_name}<br>
                            <strong>Email:</strong> ${factura.customer_email || 'N/A'}<br>
                            <strong>Estado:</strong> ${getEstadoFactura(factura.status)}
                        </div>
                        <div>
                            <strong>Total:</strong> $${parseFloat(factura.total).toFixed(2)}<br>
                            <strong>Fecha:</strong> ${factura.created_at ? new Date(factura.created_at).toLocaleDateString('es-ES') : 'N/A'}<br>
                            <strong>Vendedor:</strong> ${factura.user?.name || 'N/A'}
                        </div>
                    </div>
                    
                    ${factura.items && factura.items.length > 0 ? `
                        <h4>📦 Items de la Factura</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 8px; border: 1px solid #ddd;">Producto</th>
                                    <th style="padding: 8px; border: 1px solid #ddd;">Cantidad</th>
                                    <th style="padding: 8px; border: 1px solid #ddd;">Precio Unit.</th>
                                    <th style="padding: 8px; border: 1px solid #ddd;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${factura.items.map(item => `
                                    <tr>
                                        <td style="padding: 8px; border: 1px solid #ddd;">${item.product?.name || item.product_name || 'Producto N/A'}</td>
                                        <td style="padding: 8px; border: 1px solid #ddd;">${item.quantity}</td>
                                        <td style="padding: 8px; border: 1px solid #ddd;">$${parseFloat(item.price).toFixed(2)}</td>
                                        <td style="padding: 8px; border: 1px solid #ddd;">$${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    ` : '<p>No hay items en esta factura.</p>'}
                    
                    <div style="margin-top: 20px;">
                        <button class="btn" onclick="closeModal()">❌ Cerrar</button>
                    </div>
                </div>
            `;
            document.getElementById('facturaDetails').innerHTML = content;
        })
        .catch(error => {
            document.getElementById('facturaDetails').innerHTML = 
                `<div class="status error">Error al cargar factura: ${error.message}</div>`;
        });
}

function editFactura(id) {
    showModal('✏️ Editar Factura', `
        <div class="status info">La edición de facturas requiere una interfaz más compleja.</div>
        <div class="status info">Por ahora, use la interfaz web principal de Laravel para editar facturas.</div>
        <div style="margin-top: 20px;">
            <button class="btn" onclick="closeModal()">❌ Cerrar</button>
            <button class="btn btn-warning" onclick="viewFactura(${id})">👁️ Ver Detalles</button>
        </div>
    `);
}

function showCreateInvoiceModal() {
    const content = `
        <div class="status info">⚠️ Crear Factura Simplificada</div>
        <form onsubmit="createFactura(event)">
            <div class="form-group">
                <label>ID del Cliente:</label>
                <input type="number" name="customer_id" required>
                <small>Use el ID de un cliente existente</small>
            </div>
            <div class="form-group">
                <label>Nombre del Cliente:</label>
                <input type="text" name="customer_name" required>
            </div>
            <div class="form-group">
                <label>Email del Cliente:</label>
                <input type="email" name="customer_email" required>
            </div>
            <div class="form-group">
                <label>Total:</label>
                <input type="number" name="total" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Estado:</label>
                <select name="status">
                    <option value="draft">📝 Borrador</option>
                    <option value="sent">📧 Enviada</option>
                    <option value="paid">✅ Pagada</option>
                    <option value="cancelled">❌ Cancelada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Crear Factura</button>
            <button type="button" class="btn" onclick="closeModal()">Cancelar</button>
        </form>
    `;
    showModal('➕ Crear Nueva Factura', content);
}

function createFactura(event) {
    event.preventDefault();
    updateApiSettings();
    
    const formData = new FormData(event.target);
    const facturaData = {
        customer_id: parseInt(formData.get('customer_id')),
        customer_name: formData.get('customer_name'),
        customer_email: formData.get('customer_email'),
        total: parseFloat(formData.get('total')),
        status: formData.get('status'),
        items: [] // Factura sin items por simplicidad
    };
    
    apiClient.createFactura(facturaData);
}
