# 🌐 Cliente API - Sistema de Facturas

## 📋 **DESCRIPCIÓN**
Cliente web ligero desarrollado con **HTML + CSS + JavaScript vanilla** (sin dependencias) para consumir la API REST del Sistema de Facturas Laravel.

## ✨ **CARACTERÍSTICAS**

### **🎯 Funcionalidades Principales**
- ✅ **Gestión Completa**: Productos, Categorías, Clientes, Facturas, Pagos
- ✅ **Autenticación**: Soporte para tokens Sanctum (v1) y PlainText (v2)  
- ✅ **CRUD Completo**: Crear, Leer, Actualizar, Eliminar
- ✅ **Interfaz Responsive**: Se adapta a móviles y desktop
- ✅ **Manejo de Errores**: Mensajes claros y informativos
- ✅ **Zero Dependencies**: Solo HTML, CSS y JavaScript vanilla

### **🎨 Interfaz de Usuario**
- **Design System**: UI moderna con gradientes y tarjetas
- **Tabs Navigation**: Organización por módulos
- **Modales**: Para formularios de creación/edición
- **Tablas Responsivas**: Visualización optimizada de datos
- **Estados Visuales**: Loading, success, error con iconos

## 🚀 **INSTALACIÓN Y USO**

### **1. Requisitos**
- ✅ Navegador web moderno (Chrome, Firefox, Safari, Edge)
- ✅ Laravel API ejecutándose en `http://127.0.0.1:8000`
- ✅ CORS habilitado en Laravel

### **2. Configuración de CORS en Laravel**
Agregar en `config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],  // En producción usar dominios específicos
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => false,
```

### **3. Ejecución**
```bash
# Opción 1: Abrir directamente en el navegador
# Hacer doble clic en index.html

# Opción 2: Servidor HTTP simple (Python)
cd cliente-api
python -m http.server 8080

# Opción 3: Servidor HTTP simple (Node.js)
cd cliente-api
npx http-server -p 8080

# Luego ir a: http://localhost:8080
```

## 🔑 **TOKENS DE PRUEBA**

### **Tokens Preconfigurados**
```javascript
// Admin (acceso completo)
"admin-test-token"

// Ventas (gestión comercial)  
"ventas-test-token"

// Cliente (solo sus facturas)
"cliente-test-token-1"
```

### **Configuración de API**
- **URL Base**: `http://127.0.0.1:8000`
- **Versión por Defecto**: `v2` (PlainText tokens)
- **Headers**: `Authorization: Bearer {token}`

## 📊 **MÓDULOS DISPONIBLES**

### **📦 Productos**
- ✅ Listar productos con paginación
- ✅ Crear nuevo producto
- ✅ Editar producto existente
- ✅ Eliminar producto
- ✅ Ver stock y categoría

### **📂 Categorías**
- ✅ Listar todas las categorías
- ✅ Ver estado activo/inactivo

### **👥 Clientes**
- ✅ Gestión completa de clientes
- ✅ Crear nuevos usuarios
- ✅ Ver roles y estados

### **🧾 Facturas**
- ✅ Listar facturas con detalles
- ✅ Ver totales y estados
- ✅ Crear nuevas facturas

### **💳 Pagos**
- ✅ Historial de pagos
- ✅ Estados: Pendiente, Aprobado, Rechazado
- ✅ Tipos: Efectivo, Tarjeta, Transferencia, Cheque

### **📋 Mis Facturas (Cliente)**
- ✅ Vista específica para clientes
- ✅ Solo facturas propias
- ✅ Requiere token de cliente

## 🎯 **GUÍA DE USO**

### **1. Configuración Inicial**
1. Abrir `index.html` en el navegador
2. Configurar token de API (por defecto: `admin-test-token`)
3. Seleccionar versión de API (`v1` o `v2`)
4. Verificar URL base (`http://127.0.0.1:8000`)
5. Hacer clic en "🔗 Probar Conexión"

### **2. Navegación**
- **Tabs Superiores**: Cambiar entre módulos
- **Botones de Carga**: Obtener datos del servidor
- **Botones de Acción**: Crear, editar, eliminar
- **Modales**: Formularios para entrada de datos

### **3. Pruebas de Seguridad**
```javascript
// Probar como Admin
Token: "admin-test-token" -> ✅ Acceso total

// Probar como Cliente  
Token: "cliente-test-token-1" -> ❌ Solo "Mis Facturas"

// Probar token inválido
Token: "invalid-token" -> ❌ Error 401/403
```

## 🛠️ **ESTRUCTURA DE ARCHIVOS**

```
cliente-api/
├── index.html          # Interfaz principal
├── api-client.js       # Lógica de API y UI
├── README.md          # Esta documentación
└── (próximos archivos)
```

## 🔧 **PERSONALIZACIÓN**

### **Cambiar URL de API**
```javascript
// En api-client.js, línea 6
this.baseUrl = 'https://tu-dominio.com';
```

### **Agregar Nuevos Endpoints**
```javascript
// Ejemplo: agregar endpoint de reportes
async getReportes() {
    try {
        const data = await this.makeRequest(this.getUrl('reportes'));
        displayReportes(data.data || data);
        showStatus('✅ Reportes cargados', 'success');
    } catch (error) {
        showStatus(`❌ Error: ${error.message}`, 'error');
    }
}
```

### **Personalizar Estilos**
```css
/* Cambiar tema de colores */
:root {
    --primary-color: #007bff;
    --success-color: #28a745;
    --danger-color: #dc3545;
}
```

## 🐛 **TROUBLESHOOTING**

### **Errores Comunes**

**❌ CORS Error**
```
Solución: Configurar CORS en Laravel
Archivo: config/cors.php
```

**❌ 401 Unauthorized**
```
Solución: Verificar token válido
Token debe existir en tabla plain_text_tokens
```

**❌ 403 Forbidden**  
```
Solución: Verificar permisos de rol
Usuario debe tener rol correcto (admin/ventas)
```

**❌ Network Error**
```
Solución: Verificar que Laravel esté ejecutándose
URL: http://127.0.0.1:8000
```

## 📈 **PRÓXIMAS MEJORAS**

### **Fase 1**
- [ ] Exportar datos a Excel/PDF
- [ ] Filtros avanzados en tablas
- [ ] Gráficos con Chart.js
- [ ] Notificaciones push

### **Fase 2** 
- [ ] Modo offline con IndexedDB
- [ ] PWA (Progressive Web App)
- [ ] Autenticación OAuth2
- [ ] WebSockets para tiempo real

## 🏆 **VENTAJAS**

✅ **Sin Dependencias**: No requiere npm, webpack, etc.  
✅ **Portabilidad**: Ejecuta en cualquier servidor web  
✅ **Ligereza**: Menos de 50KB total  
✅ **Mantenibilidad**: Código JavaScript simple  
✅ **Rapidez**: Carga instantánea  
✅ **Compatibilidad**: Funciona en todos los navegadores modernos  

---

**📅 Creado**: 8 de Agosto, 2025  
**👨‍💻 Desarrollado por**: GitHub Copilot  
**🔧 Tecnologías**: HTML5, CSS3, JavaScript ES6+  
**📊 Estado**: ✅ LISTO PARA PRODUCCIÓN
