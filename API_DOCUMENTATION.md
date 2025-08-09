# 📚 API REST - Sistema de Facturación Seguro

## 🔐 Autenticación

### Versiones Disponibles:
- **v1**: Tokens Sanctum hasheados (SEGURO) - `auth:sanctum`
- **v2**: Tokens texto plano (DESARROLLO) - `auth.plaintext`

### Headers Requeridos:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## 👥 API de Clientes

### Listar Clientes
```
GET /api/v2/clientes
```
**Parámetros opcionales:**
- `status`: active|inactive
- `search`: Buscar por nombre, email o teléfono
- `per_page`: Elementos por página (máx 100)

### Crear Cliente
```
POST /api/v2/clientes
```
**Body:**
```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "status": "active"
}
```

### Ver Cliente
```
GET /api/v2/clientes/{id}
```

### Actualizar Cliente
```
PUT /api/v2/clientes/{id}
```
**Body:** (password es opcional)
```json
{
  "name": "Juan Pérez Actualizado",
  "email": "juan.nuevo@example.com",
  "status": "active"
}
```

### Eliminar Cliente
```
DELETE /api/v2/clientes/{id}
```

---

## 📦 API de Productos

### Listar Productos
```
GET /api/v2/productos
```
**Parámetros opcionales:**
- `category_id`: Filtrar por categoría
- `status`: active|inactive
- `search`: Buscar por nombre, descripción o SKU
- `with_stock`: true (solo productos con stock)
- `per_page`: Elementos por página

### Crear Producto
```
POST /api/v2/productos
```
**Body:**
```json
{
  "name": "Laptop HP",
  "description": "Laptop HP Pavilion 15'",
  "sku": "HP-LAP-001",
  "price": 899.99,
  "stock": 10,
  "category_id": 1,
  "status": "active",
  "tax_rate": 12
}
```

### Ver Producto
```
GET /api/v2/productos/{id}
```

### Actualizar Producto
```
PUT /api/v2/productos/{id}
```

### Eliminar Producto
```
DELETE /api/v2/productos/{id}
```

### Obtener Categorías
```
GET /api/v2/categorias
```

---

## 🧾 API de Facturas

### Listar Facturas
```
GET /api/v2/facturas
```
**Parámetros opcionales:**
- `status`: active|pending|paid|cancelled
- `customer_id`: Filtrar por cliente
- `user_id`: Filtrar por vendedor
- `search`: Buscar por número, cliente
- `date_from`: Fecha desde (YYYY-MM-DD)
- `date_to`: Fecha hasta (YYYY-MM-DD)

### Crear Factura
```
POST /api/v2/facturas
```
**Body:**
```json
{
  "customer_id": 1,
  "customer_name": "Juan Pérez",
  "customer_email": "juan@example.com",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "unit_price": 899.99,
      "tax_rate": 12
    },
    {
      "product_id": 2,
      "quantity": 1
    }
  ]
}
```

### Ver Factura
```
GET /api/v2/facturas/{id}
```

### Actualizar Factura
```
PUT /api/v2/facturas/{id}
```
**Nota:** Solo facturas con status "active"

### Cancelar Factura
```
DELETE /api/v2/facturas/{id}
```
**Nota:** Cancela la factura y restaura stock

---

## 💳 API de Pagos

### Listar Facturas Pendientes
```
GET /api/v2/facturas-pendientes
```

### Crear Pago
```
POST /api/v2/pagos
```
**Body:**
```json
{
  "invoice_id": 1,
  "tipo_pago": "tarjeta",
  "monto": 150.00,
  "numero_transaccion": "TXN1691234567890",
  "observacion": "Pago completo de la factura"
}
```

### Historial de Pagos
```
GET /api/v2/mis-pagos
```

---

## 📋 API de Usuario

### Información del Usuario
```
GET /api/v2/user
```

### Mis Facturas (Clientes)
```
GET /api/v2/mis-facturas
```

---

## 🔄 Respuestas de la API

### Éxito
```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": {...}
}
```

### Error
```json
{
  "success": false,
  "message": "Error en la operación",
  "error": "Detalle del error",
  "errors": {...} // Solo en errores de validación
}
```

### Códigos de Estado
- `200`: OK
- `201`: Creado
- `404`: No encontrado
- `422`: Errores de validación
- `500`: Error interno

---

## 🧪 Testing con Postman

### Token de Prueba
```
Bearer cliente_api_token_205922
```

### Ejemplos de URLs Base
- **Desarrollo:** `http://localhost:8000/api/v2/`
- **Producción:** `https://tudominio.com/api/v2/`

### Colección Postman
Importa y usa las siguientes rutas para probar la API completa.
