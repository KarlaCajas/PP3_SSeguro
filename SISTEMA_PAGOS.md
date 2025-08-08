# Sistema de Pagos de Facturas

## Descripción General

Este documento describe la implementación del sistema de pagos de facturas que permite a los clientes registrar pagos vía API REST y a los usuarios con rol "pagos" validar estos pagos desde la interfaz web.

## Flujo de Funcionamiento

### 1. Autenticación y Registro de Pago (API REST)
- **Usuario**: Cliente con rol "cliente"
- **Autenticación**: Laravel Sanctum (token-based)
- **Endpoint**: `POST /api/pagos`
- **Funcionalidad**: El cliente registra el pago de una factura

### 2. Validación de Pago (Web Interface)
- **Usuario**: Validador con rol "pagos" 
- **Autenticación**: Laravel Breeze (sesión web)
- **Funcionalidad**: Revisar, aprobar o rechazar pagos pendientes

## Endpoints API

### Autenticación
Todos los endpoints requieren autenticación con Sanctum:
```
Authorization: Bearer {token}
```

### 1. Obtener Facturas Pendientes de Pago
```http
GET /api/facturas-pendientes
```

**Respuesta exitosa:**
```json
{
    "message": "Facturas pendientes obtenidas exitosamente",
    "data": [
        {
            "id": 1,
            "invoice_number": "FAC-20250807-0001",
            "total": 150.00,
            "total_pagado": 0.00,
            "saldo_pendiente": 150.00,
            "status": "active",
            "created_at": "2025-08-07T10:00:00Z",
            "tiene_pagos_pendientes": false
        }
    ]
}
```

### 2. Registrar Pago
```http
POST /api/pagos
```

**Parámetros requeridos:**
```json
{
    "invoice_id": 1,
    "tipo_pago": "transferencia",
    "monto": 150.00,
    "numero_transaccion": "TXN123456789",
    "observacion": "Pago completo de factura"
}
```

**Tipos de pago válidos:**
- `efectivo`
- `tarjeta`
- `transferencia`
- `cheque`

**Respuesta exitosa:**
```json
{
    "message": "Pago registrado exitosamente. Está pendiente de validación.",
    "data": {
        "payment_id": 1,
        "invoice_id": 1,
        "monto": 150.00,
        "tipo_pago": "Transferencia",
        "numero_transaccion": "TXN123456789",
        "estado": "Pendiente",
        "created_at": "2025-08-07T10:30:00Z"
    }
}
```

### 3. Obtener Historial de Pagos del Cliente
```http
GET /api/mis-pagos
```

**Respuesta exitosa:**
```json
{
    "message": "Historial de pagos obtenido exitosamente",
    "data": [
        {
            "id": 1,
            "invoice_number": "FAC-20250807-0001",
            "monto": 150.00,
            "tipo_pago": "Transferencia",
            "numero_transaccion": "TXN123456789",
            "estado": "Aprobado",
            "observacion": "Pago completo de factura",
            "created_at": "2025-08-07T10:30:00Z",
            "validated_at": "2025-08-07T11:00:00Z",
            "validado_por": "Validador de Pagos"
        }
    ]
}
```

## Rutas Web (Interfaz de Validación)

### Acceso Requerido
- **Rol**: `pagos` o `admin`
- **Autenticación**: Sesión web (Laravel Breeze)

### Rutas Disponibles
- `GET /payments` - Listado de pagos pendientes
- `GET /payments/{payment}` - Detalles de un pago específico
- `POST /payments/{payment}/aprobar` - Aprobar un pago
- `POST /payments/{payment}/rechazar` - Rechazar un pago
- `GET /payments-historial` - Historial de pagos procesados

## Estados de Pago

### Estados del Pago
- **pendiente**: Pago registrado, esperando validación
- **aprobado**: Pago validado y aceptado
- **rechazado**: Pago validado pero rechazado

### Estados de Factura
- **active/pending**: Factura activa, pendiente de pago
- **paid**: Factura completamente pagada
- **cancelled**: Factura cancelada

## Validaciones de Seguridad

### API (Sanctum)
1. **Autenticación requerida**: Token válido
2. **Autorización de rol**: Solo usuarios con rol "cliente"
3. **Propiedad de factura**: El cliente solo puede pagar sus propias facturas
4. **Estado de factura**: No se pueden pagar facturas canceladas o ya pagadas
5. **Validación de monto**: El monto no puede exceder el saldo pendiente

### Web Interface
1. **Autenticación requerida**: Sesión web válida
2. **Autorización de rol**: Solo usuarios con rol "pagos" o "admin"
3. **Estado de pago**: Solo se pueden procesar pagos pendientes
4. **Logs de actividad**: Todas las acciones se registran

## Estructura de Base de Datos

### Tabla: payments
```sql
CREATE TABLE payments (
    id BIGINT PRIMARY KEY,
    invoice_id BIGINT REFERENCES invoices(id) ON DELETE CASCADE,
    tipo_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'cheque'),
    monto DECIMAL(10,2),
    numero_transaccion VARCHAR(255),
    observacion TEXT,
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    pagado_por BIGINT REFERENCES users(id) ON DELETE CASCADE,
    validado_por BIGINT REFERENCES users(id) ON DELETE SET NULL,
    validated_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Índices para Optimización
- `(estado, created_at)` - Para consultas de pagos pendientes
- `(invoice_id, estado)` - Para consultas por factura
- `pagado_por` - Para consultas por cliente

## Roles y Permisos

### Roles Definidos
- **cliente**: Puede registrar pagos vía API
- **pagos**: Puede validar pagos vía web
- **admin**: Acceso completo a ambas funcionalidades

### Credenciales de Prueba
```
# Usuario Validador de Pagos
Email: pagos@barespe.com
Password: password123
Rol: pagos

# Usuario Cliente
Email: cliente@barespe.com  
Password: password123
Rol: cliente
```

## Ejemplo de Uso Completo

### 1. Cliente registra pago vía API
```bash
curl -X POST http://localhost:8000/api/pagos \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice_id": 1,
    "tipo_pago": "transferencia",
    "monto": 150.00,
    "numero_transaccion": "TXN123456789",
    "observacion": "Pago completo"
  }'
```

### 2. Validador accede al sistema web
1. Login en `/login` con credenciales del rol "pagos"
2. Ir a Dashboard → "Validación de Pagos" → "Pagos Pendientes"
3. Revisar detalles del pago
4. Aprobar o rechazar el pago

### 3. Resultado
- **Si se aprueba**: Factura se marca como "paid"
- **Si se rechaza**: Factura permanece "pending", cliente puede registrar nuevo pago

## Logs y Auditoría

### Actividades Registradas
- Registro de pagos por clientes (API)
- Aprobación/rechazo de pagos (Web)
- Cambios de estado de facturas
- Accesos al sistema de validación

### Ubicación de Logs
- **Laravel Logs**: `storage/logs/laravel.log`
- **Activity Logs**: Tabla `activity_logs` (si está configurada)

---

**Fecha de Implementación**: 7 de agosto de 2025
**Versión**: 1.0
**Responsable**: Sistema BarEspe VentasPro
